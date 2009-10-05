class Tag < ActiveRecord::Base

  belongs_to :network
  belongs_to :publisher
  has_many :tag_options

  accepts_nested_attributes_for :tag_options, :allow_destroy => true

  #TODO: validate publisherid once accounts are set up
  validates_format_of :size, :with => /[0-9]{1,3}x[0-9]{1,3}/
  validates_uniqueness_of :tag_name, :scope => :publisher_id
  validates_presence_of :tag_name, :network_id, :size
  validates_inclusion_of :enabled, :in => [true, false]
  validates_inclusion_of :always_fill, :in => [true, false]
  validates_numericality_of :tier, :only_integer => true, :greater_than_or_equal_to => 0, :less_than_or_equal_to => 10, :allow_nil => true
  validates_numericality_of :sample_rate, :greater_than_or_equal_to => 0, :less_than => 100, :allow_nil => true
  validates_numericality_of :frequency_cap, :only_integer => true, :greater_than_or_equal_to => 0, :less_than => 1000, :allow_nil => true
  validates_numericality_of :rejection_time, :only_integer => true, :greater_than_or_equal_to => 0, :less_than => 1440, :allow_nil => true
  validates_numericality_of :value, :greater_than_or_equal_to => 0, :less_than => 100 


   def enabled_s 
      enabled ? "Yes" : "No"
   end

   def always_fill_s 
      always_fill ? "Yes" : "No"
   end


end
