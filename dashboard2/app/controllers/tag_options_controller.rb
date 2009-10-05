class TagOptionsController < ApplicationController
  before_filter :require_user

  def index
    @tag_options = TagOption.all
  end
  
  def show
    @tag_option = TagOption.find(params[:id])
  end
  
  def new
    @tag_option = TagOption.new
  end
  
  def create
    @tag_option = TagOption.new(params[:tag_option])
    if @tag_option.save
      flash[:notice] = "Successfully created tag option."
      redirect_to @tag_option
    else
      render :action => 'new'
    end
  end
  
  def edit
    @tag_option = TagOption.find(params[:id])
  end
  
  def update
    @tag_option = TagOption.find(params[:id])
    if @tag_option.update_attributes(params[:tag_option])
      flash[:notice] = "Successfully updated tag option."
      redirect_to @tag_option
    else
      render :action => 'edit'
    end
  end
  
  def destroy
    @tag_option = TagOption.find(params[:id])
    @tag_option.destroy
    flash[:notice] = "Successfully destroyed tag option."
    redirect_to tag_options_url
  end
end
