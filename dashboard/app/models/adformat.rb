class Adformat < ActiveRecord::Base
  validates_presence_of :format_name, :size
  validates_uniqueness_of :format_name 
  validates_format_of :size, :with => /^[0-9]{1,3}x[0-9]{1,3}$/

    def name_with_size
      "#{format_name} (#{size})" 
    end

    def name
      "Ad Format"
    end

end
