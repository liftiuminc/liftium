class AdformatsController < ApplicationController
  # GET /adformats
  # GET /adformats.xml
  def index
    @adformats = Adformat.all

    respond_to do |format|
      format.html # index.html.erb
      format.xml  { render :xml => @adformats }
    end
  end

  # GET /adformats/1
  # GET /adformats/1.xml
  def show
    @adformat = Adformat.find(params[:id])

    respond_to do |format|
      format.html # show.html.erb
      format.xml  { render :xml => @adformat }
    end
  end

  # GET /adformats/new
  # GET /adformats/new.xml
  def new
    @adformat = Adformat.new

    respond_to do |format|
      format.html # new.html.erb
      format.xml  { render :xml => @adformat }
    end
  end

  # GET /adformats/1/edit
  def edit
    @adformat = Adformat.find(params[:id])
  end

  # POST /adformats
  # POST /adformats.xml
  def create
    @adformat = Adformat.new(params[:adformat])

    respond_to do |format|
      if @adformat.save
        flash[:notice] = 'Adformat was successfully created.'
        format.html { redirect_to(@adformat) }
        format.xml  { render :xml => @adformat, :status => :created, :location => @adformat }
      else
        format.html { render :action => "new" }
        format.xml  { render :xml => @adformat.errors, :status => :unprocessable_entity }
      end
    end
  end

  # PUT /adformats/1
  # PUT /adformats/1.xml
  def update
    @adformat = Adformat.find(params[:id])

    respond_to do |format|
      if @adformat.update_attributes(params[:adformat])
        flash[:notice] = 'Adformat was successfully updated.'
        format.html { redirect_to(@adformat) }
        format.xml  { head :ok }
      else
        format.html { render :action => "edit" }
        format.xml  { render :xml => @adformat.errors, :status => :unprocessable_entity }
      end
    end
  end

  # DELETE /adformats/1
  # DELETE /adformats/1.xml
  def destroy
    @adformat = Adformat.find(params[:id])
    @adformat.destroy

    respond_to do |format|
      format.html { redirect_to(adformats_url) }
      format.xml  { head :ok }
    end
  end
end
