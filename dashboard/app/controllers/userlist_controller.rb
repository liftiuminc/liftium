class UserlistController < ApplicationController
  active_scaffold :user
  active_scaffold :user do |config|
    config.columns = [:email, :admin, :current_login_at, :last_login_at, :publisher ]
    config.create.columns = [:email, :admin, :publisher ]
    config.update.columns = [:email, :admin, :publisher ]
    config.columns[:admin].form_ui = :checkbox
  end
end
