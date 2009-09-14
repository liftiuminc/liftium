class AdformatsController < ApplicationController
  before_filter :require_user
  active_scaffold :adformat
end
