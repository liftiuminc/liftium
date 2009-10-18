class TagTargetsController < ApplicationController
  if Rails.configuration.environment != "test"
     before_filter :require_user
  end

  def index
    @tag_targets = TagTarget.all
  end
  
  def show
    @tag_target = TagTarget.find(params[:id])
  end
  
  def new
    @tag_target = TagTarget.new
  end
  
  def create
    @tag_target = TagTarget.new(params[:tag_target])
    if @tag_target.save
      flash[:notice] = "Successfully created tag target."
      redirect_to @tag_target
    else
      render :action => 'new'
    end
  end
  
  def edit
    @tag_target = TagTarget.find(params[:id])
  end
  
  def update
    @tag_target = TagTarget.find(params[:id])
    if @tag_target.update_attributes(params[:tag_target])
      flash[:notice] = "Successfully updated tag target."
      redirect_to @tag_target
    else
      render :action => 'edit'
    end
  end
  
  def destroy
    @tag_target = TagTarget.find(params[:id])
    @tag_target.destroy
    flash[:notice] = "Successfully destroyed tag target."
    redirect_to tag_targets_url
  end
end
