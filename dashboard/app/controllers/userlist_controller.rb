class UserlistController < ApplicationController
  before_filter :require_user

  active_scaffold :user
  active_scaffold :user do |config|
    config.columns = [:email, :publisher, :admin, :current_login_at, :current_login_ip, :last_login_at, :last_login_ip ]
    config.create.columns = [:email, :password, :password_confirmation, :admin, :publisher ]
    config.update.columns = [:email, :admin, :publisher ]
    config.columns[:admin].form_ui = :checkbox
  end
end


# Not working for some reason
#module UserlistHelper
#   def password_column(record)
#      password_field "record", "password", :class => "email-input text-input", :size => 30
#   end
#   def password_confirmation_column(record)
#      password_field "record", "password_confirmation", :class => "email-input text-input", :size => 30
#   end
#end
