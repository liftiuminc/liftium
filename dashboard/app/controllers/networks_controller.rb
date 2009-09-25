class NetworksController < ApplicationController
  before_filter :require_user
  active_scaffold

  active_scaffold :network do |config|
    config.label = "Ad Networks"
 #   config.columns = [:network_name, :enabled, :pay_type, :always_fill, :supports_threshold ]
    list.sorting = {:network_name => 'ASC'}
  #  columns[:phone].description = "(Format: ###-###-####)"
  end

end
