class PublishersController < ApplicationController
  before_filter :require_user
  active_scaffold :publisher
end
