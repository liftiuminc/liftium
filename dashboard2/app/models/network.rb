class Network < ActiveRecord::Base
  attr_accessible :network_name, :website, :pay_type, :enabled, :supports_threshold, :default_always_fill, :us_only, :comments, :contact_info, :billing_info, :brand_safety_level, :tag_template, :scraping_instructions

  @all_pay_types = ["Per Click", "Per Impression", "Affliate" ]
  has_many :network_options
  validates_uniqueness_of :network_name
  validates_presence_of :network_name, :pay_type
  validates_inclusion_of :enabled, :in => [true, false]
  validates_inclusion_of :default_always_fill, :in => [true, false]
  validates_inclusion_of :supports_threshold, :in => [true, false]
  validates_inclusion_of :pay_type, :in => @all_pay_types, :message => "must be one of: " + @all_pay_types.join(', ')

   def enabled_s 
      enabled ? "Yes" : "No"
   end

   def enabled_h
      enabled ? '<input type="checkbox" disabled="true" checked="true"/>' : '<input type="checkbox" disabled="true"/>'
   end

   def supports_threshold_s
      supports_threshold ? "Yes" : "No"
   end

   def supports_threshold_h
      supports_threshold ? '<input type="checkbox" disabled="true" checked="true"/>' : '<input type="checkbox" disabled="true"/>'
   end

   def default_always_fill_s 
      default_always_fill ? "Yes" : "No"
   end

   def default_always_fill_h
      default_always_fill ? '<input type="checkbox" disabled="true" checked="true"/>' : '<input type="checkbox" disabled="true"/>'
   end

   def us_only_s 
      us_only ? "Yes" : "No"
   end

   def us_only_h
      us_only ? '<input type="checkbox" disabled="true" checked="true"/>' : '<input type="checkbox" disabled="true"/>'
   end

   def pay_types 
        ["Per Click", "Per Impression", "Affliate" ]
   end
  

end
