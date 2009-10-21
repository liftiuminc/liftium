class HomesController < ApplicationController
  before_filter :require_user, :only => ["admin", "publisher"]

  def index
    if current_user 
      if current_user.admin
        render :admin
      elsif !current_user.admin && current_user.publisher
        render :publisher
      end
    end
  end

  def admin
    if !current_user.admin 
      render(:file => 'public/403.html', :status => 403) 
    end 
  end

  def publisher
  end

end
