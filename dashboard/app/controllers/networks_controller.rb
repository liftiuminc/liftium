class NetworksController < ApplicationController
  before_filter :require_user
  active_scaffold

  active_scaffold :network do |config|
    config.label = "Ad Networks"

    config.columns = [:network_name, :enabled, :pay_type, :always_fill, :supports_threshold ]
    config.create.columns = [:network_name, :pay_type, :enabled, :always_fill, :supports_threshold, :tag_template, :network_options ]
    config.update.columns = [:network_name, :pay_type, :enabled, :always_fill, :supports_threshold, :tag_template, :network_options ]
    list.sorting = {:network_name => 'ASC'}

    config.columns[:enabled].form_ui = :checkbox
    config.columns[:always_fill].form_ui = :checkbox
    config.columns[:supports_threshold].form_ui = :checkbox

    config.columns[:tag_template].description = "<br />Copy Paste tag here. Note that you may use macros of <b>%@width@%, %@height@%, %@size@%</b>.<br />Macros also available one for each network option. Ie. %@account_id@%"
    config.columns[:supports_threshold].description = "Default tags allowed?"
    config.nested.add_link("Network Options", [:network_options])

  end
end
