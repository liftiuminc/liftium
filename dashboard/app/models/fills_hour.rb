class FillsHour < ActiveRecord::Base

  set_table_name "fills_hour"
  belongs_to :tag
end
