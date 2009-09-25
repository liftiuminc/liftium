class PublishersController < ApplicationController
  before_filter :require_user
  active_scaffold :publisher
  active_scaffold :publisher do |config|
    config.columns = [:publisher_name, :website ]
  end
   
end
