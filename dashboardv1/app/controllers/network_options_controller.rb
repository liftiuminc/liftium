class NetworkOptionsController < ApplicationController
  before_filter :require_user
  active_scaffold
end
