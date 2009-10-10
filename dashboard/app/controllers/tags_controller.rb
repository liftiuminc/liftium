class TagsController < ApplicationController
  before_filter :require_user

  def index
    # FIXME: This belongs in the model
    # Could I have tried to do this with native ActiveRecord find? Yep.

    query = []
    query.push("SELECT * FROM tags WHERE 1=1");

    if (params[:include_disabled].blank?)
       query[0] += " AND enabled = ?"
       query.push(true)
    end

    if (! params[:publisher_id].blank?)
       query[0] += " AND publisher_id = ?"
       query.push(params[:publisher_id].to_i)
    end

    if (! params[:network_id].blank?)
       query[0] += " AND network_id = ?"
       query.push(params[:network_id].to_i)
    end

    if (! params[:size].blank?)
       query[0] += " AND size = ?"
       query.push(params[:size])
    end

    if (! params[:name_search].blank?)
       query[0] += " AND tag_name like ?"
       query.push('%' + params[:name_search] + '%')
    end

    if params[:debug]
       flash[:notice] = "Query: " + query.inspect
    end

    @tags = Tag.find_by_sql query
    if @tags.length < 1
      flash[:notice] = "No matching tags found"
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
    @tag.tag_name = "Copy of #{@tag.tag_name}"
    @tag.tag_options = @tag_orig.tag_options
    render :action => 'edit'
  end
  
  def update
    @tag = Tag.find(params[:id])
    if @tag.update_attributes(params[:tag])
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
      flash[:notice] = "html_preview expects either html or id"
      redirect_to @tag
    end
  end

end
