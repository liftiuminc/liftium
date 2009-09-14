class AdformatsController < ApplicationController
  before_filter :require_user
  active_scaffold :adformat
  active_scaffold :adformat do |config|
    config.columns = [:format_name, :width, :height ]
  end
end
