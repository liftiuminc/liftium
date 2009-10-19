class UserSessionsController < ApplicationController
  if Rails.configuration.environment != "test"
    before_filter :require_no_user, :only => [:new, :create]
    before_filter :require_user, :only => :destroy
  end
  
  def new
    @user_session = UserSession.new
  end
  
  def create
    @user_session = UserSession.new(params[:user_session])
    if @user_session.save
      flash[:notice] = "Login successful!"
      redirect_back_or_default "/"
    else
      render :action => :new
    end
  end
  
  def destroy
    current_user_session.destroy
    flash[:notice] = "Logout successful!"
    redirect_to "/"
  end
end
