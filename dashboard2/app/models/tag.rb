class Tag < ActiveRecord::Base
  attr_accessible :tag_name, :value_in_cents, :enabled, :always_fill, :sample_rate, :tier, :frequency_cap, :rejection_time, :tag, :tag_options_attributes

  belongs_to :network
  belongs_to :publisher
  belongs_to :adformat
  has_many :tag_options

  accepts_nested_attributes_for :tag_options, :allow_destroy => true

end
