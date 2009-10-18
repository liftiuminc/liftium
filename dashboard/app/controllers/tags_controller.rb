class TagsController < ApplicationController
  before_filter :require_user

  def index
    @tags = Tag.new.search(params) 

    if params[:debug]
      flash[:notice] = "<span style='font-size:smaller'>SQL: " + Tag.new.search_sql(params).inspect + "</span>"
    end

    if @tags.length < 1
      flash[:warning] = "No matching tags found"
    end
  end

  
  def show
    @tag = Tag.find(params[:id])
  end
  
  def select_network
    # Get a list of enabled networks
    @networks = Network.find :all, :conditions => {:enabled => true}
    @tag = Tag.new
  end

  def new
    if !params[:network_id] 
      flash[:notice] = "Please select a network to continue"
      redirect_to :action => 'select_network'
    else 
    
      # Get a list of enabled networks
      @networks = Network.find :all, :conditions => {:enabled => true}
     
      # Get the list of publishers for admin users
      @publishers = Publisher.find :all;
      @tag = Tag.new
      @tag.network_id = params[:network_id]
      @tag.tag_options.build
      @tag.always_fill = @tag.network.default_always_fill

    end
  end
  
  def create
    @tag = Tag.new(params[:tag])

    if @tag.save

      ### any associated notes? See FB 24
      if params[:note]
        comment = Comment.new(  :title   => params[:tag][:tag_name],
                                :comment => params[:note][:tag] )

        @tag.add_comment comment
      end  

      ### is the network US only? then set the tag_target as well. See FB 36
      if @tag.network.us_only?
        tt = TagTarget.new( :key_name => 'country', :key_value => 'us' );
        @tag.update_attributes( :tag_targets => [tt] )
      end

      flash[:notice] = "Successfully created tag."
      redirect_to @tag
    else
      render :action => 'new'
    end
  end
  
  def edit
    # Get a list of enabled networks
    @networks = Network.find :all, :conditions => {:enabled => true}
   
    # Get the list of publishers for admin users
    @publishers = Publisher.find :all;
    @tag = Tag.find(params[:id])
  end

  def copy
    # Get a list of enabled networks
    @networks = Network.find :all, :conditions => {:enabled => true}
   
    # Get the list of publishers for admin users
    @publishers = Publisher.find :all;

    @tag_orig = Tag.find(params[:id])
    @tag = @tag_orig.clone
    
    ### add the date to the name, as names are unique and an error will
    ### occur on the 2nd copy otherwise
    @tag.tag_name = "Copy of #{@tag.tag_name} made at " + DateTime.now.to_s
    
    ### clone the options    
    options = @tag_orig.tag_options.map { |to| 
                    c = to.clone 
                    c.tag_id = @tag.id
                    c
                }
    @tag.update_attributes( :tag_options => options )

    ### clone the targets
    targets = @tag_orig.tag_targets.map { |tt|
                    c = tt.clone
                    c.tag_id = @tag.id
                    c
                }
    @tag.update_attributes( :tag_targets => targets );                

    render :action => 'edit'
  end
  
  def update
    @tag = Tag.find(params[:id])
    if @tag.update_attributes(params[:tag])

      ### any associated notes? See FB 24
      if params[:note] 

        ### if we already have a comment, update it
        if !@tag.comments.empty?
            @tag.comments[0].update_attributes( :comment => params[:note][:tag] )
            
        ### otherwise, create a new one    
        else 
          comment = Comment.new(  :title   => params[:tag][:tag_name],
                                  :comment => params[:note][:tag] )

          @tag.add_comment comment
        end          
      end  
      
      flash[:notice] = "Successfully updated tag."
      redirect_to tags_url
    else
      render :action => 'edit'
    end
  end
  
  def destroy
    @tag = Tag.find(params[:id])
    @tag.destroy
    flash[:notice] = "Successfully destroyed tag."
    redirect_to tags_url
  end

  def generator 
    if params[:id]
      @tag = Tag.find(params[:id])
    else 
      @tag = Tag.new
    end
  end

  def html_preview 
    if params[:id]
      @tag = Tag.find(params[:id])
      render :action => :html_preview, :layout => "bare"
    elsif params[:html]
      @tag = Tag.new
      @tag.tag = params[:html]
      render :action => :html_preview, :layout => "bare"
    else 
      flash[:error] = "html_preview expects either html or id"
      redirect_to @tag
    end
  end

end
