class AdFormatsController < ApplicationController
  if Rails.configuration.environment != "test" 
     before_filter :require_user
  end

  def index
    @ad_formats = AdFormat.all
  end
  
  def show
    @ad_format = AdFormat.find(params[:id])
  end
  
  def new
    @ad_format = AdFormat.new
  end
  
  def create
    @ad_format = AdFormat.new(params[:ad_format])
    if @ad_format.save
      flash[:notice] = "Successfully created ad format."
      redirect_to @ad_format
    else
      render :action => 'new'
    end
  end
  
  def edit
    @ad_format = AdFormat.find(params[:id])
  end
  
  def update
    @ad_format = AdFormat.find(params[:id])
    if @ad_format.update_attributes(params[:ad_format])
      flash[:notice] = "Successfully updated ad format."
      redirect_to ad_formats_url
    else
      render :action => 'edit'
    end
  end
  
  def destroy
    @ad_format = AdFormat.find(params[:id])
    @ad_format.destroy
    flash[:notice] = "Successfully destroyed ad format."
    redirect_to ad_formats_url
  end
end
