class UsersController < ApplicationController
 # before_filter :require_no_user, :only => [:new, :create]
  before_filter :require_user, :only => [:show, :edit, :update, :index]

  def index
    @users = User.all
  end
  
  def show
    if params[:id] 
      @user = User.find(params[:id])
    else 
      @user = current_user
    end
  end
  
  def new
    @user = User.new
  end
  
  def create
    @user = User.new(params[:user])
    if @user.save
      flash[:notice] = "Successfully created user."
      redirect_to @user
    else
      render :action => 'new'
    end
  end
  
  def edit
    if params[:id] 
      @user = User.find(params[:id])
    else 
      @user = current_user
    end
  end
  
  def update
    @user = User.find(params[:id])
    if @user.update_attributes(params[:user])
      flash[:notice] = "Successfully updated user."
      redirect_to @user
    else
      render :action => 'edit'
    end
  end
  
  def destroy
    @user = User.find(params[:id])
    @user.destroy
    flash[:notice] = "Successfully destroyed user."
    redirect_to users_url
  end
end
