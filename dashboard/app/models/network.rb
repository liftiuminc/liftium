class Network < ActiveRecord::Base
  has_many :network_options
  @paytypes = Array["Per Click", "Per Impression", "Affliate" ]
  validates_uniqueness_of :network_name
  validates_presence_of :network_name, :pay_type
  validates_inclusion_of :enabled, :in => [true, false]
  validates_inclusion_of :always_fill, :in => [true, false]
  validates_inclusion_of :supports_threshold, :in => [true, false]
  validates_inclusion_of :pay_type, :in => @paytypes, :message => "Pay type must be one of: " + @paytypes.join(', ')

  # So that Active Scaffolde displays the correct name
  def name
    "#{network_name}"
  end

end
