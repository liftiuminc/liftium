class WelcomeController < ApplicationController
  def index
    if current_user 
      render :index
    else 
      render :hello
    end
  end

  def hello
  end

end
