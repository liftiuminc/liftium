class NetworkTagOptionsController < ApplicationController
  before_filter :require_user

  def index
    @network_tag_options = NetworkTagOption.all
  end
  
  def show
    @network_tag_option = NetworkTagOption.find(params[:id])
  end
  
  def new
    @network_tag_option = NetworkTagOption.new
  end
  
  def create
    @network_tag_option = NetworkTagOption.new(params[:network_tag_option])
    if @network_tag_option.save
      flash[:notice] = "Successfully created network tag option."
      redirect_to @network_tag_option
    else
      render :action => 'new'
    end
  end
  
  def edit
    @network_tag_option = NetworkTagOption.find(params[:id])
  end
  
  def update
    @network_tag_option = NetworkTagOption.find(params[:id])
    if @network_tag_option.update_attributes(params[:network_tag_option])
      flash[:notice] = "Successfully updated network tag option."
      redirect_to network_tag_options_url
    else
      render :action => 'edit'
    end
  end
  
  def destroy
    @network_tag_option = NetworkTagOption.find(params[:id])
    @network_tag_option.destroy
    flash[:notice] = "Successfully destroyed network tag option."
    redirect_to network_tag_options_url
  end
end
