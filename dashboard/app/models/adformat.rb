class Adformat < ActiveRecord::Base
  belongs_to :tag

  validates_presence_of :format_name, :height, :width
  validates_uniqueness_of :format_name 
  validates_numericality_of :height, :only_integer => true, :greater_than_or_equal_to => 0, :less_than_or_equal_to => 1500
  validates_numericality_of :width, :only_integer => true, :greater_than_or_equal_to => 0, :less_than_or_equal_to => 1500

    def size
      "#{width}x#{height}"
    end

    def name_with_size
      "#{format_name} (#{size})" 
    end

end
