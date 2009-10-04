class Network < ActiveRecord::Base
  attr_accessible :network_name, :website, :pay_type, :enabled, :supports_threshold, :default_always_fill, :us_only, :comments, :contact_info, :billing_info, :brand_safety_level, :tag_template, :scraping_instructions

  @pay_types = ["Per Click", "Per Impression", "Affliate" ]
  has_many :network_options
  validates_uniqueness_of :network_name
#  validates_presence_of :network_name, :pay_type
#  validates_inclusion_of :enabled, :in => [true, false]
#  validates_inclusion_of :default_always_fill, :in => [true, false]
#  validates_inclusion_of :supports_threshold, :in => [true, false]
#  validates_inclusion_of :pay_type, :in => @pay_types, :message => "must be one of: " + @pay_types.join(', ')

end
