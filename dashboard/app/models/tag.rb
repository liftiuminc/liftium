class Tag < ActiveRecord::Base

  belongs_to :network
  belongs_to :publisher
  has_many :tag_options
  has_many :tag_targets

  accepts_nested_attributes_for :tag_options, :allow_destroy => true, :reject_if => proc { |a| a['option_name'].blank? || a['option_value'].blank? }
  accepts_nested_attributes_for :tag_targets, :allow_destroy => true, :reject_if => proc { |a| a['key_name'].blank? || a['key_value'].blank?}

  #TODO: validate publisherid once accounts are set up
  validates_format_of :size, :with => /[0-9]{1,3}x[0-9]{1,3}/
  validates_uniqueness_of :tag_name, :scope => :publisher_id
  validates_presence_of :tag_name, :network, :size, :publisher
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

   # db returns 0.1. we want this to be 0.10
   def value_s 
      sprintf( "%.2f", value)
   end

   def html 
      if tag 
	"#{tag}"
      else 
        # TODO: Network tag options expansion
	"#{tag.network.tag_template}"
      end
   end

   def width
     @d = size.to_s.split("x")
     @d[0] || 0
   end

   def height
     @d = size.to_s.split("x")
     @d[1] || 0
   end

   def css_size 
     "width:#{width}px;height:#{height}px;"
   end

   def preview_url 
     env = Rails.configuration.environment
     if env == "development" || env == "dev_mysql"
	"http://delivery.dev.liftium.com/tag?tag_id=#{id}"
     else 
	"http://delivery.liftium.com/tag?tag_id=#{id}"
     end
   end 
end
