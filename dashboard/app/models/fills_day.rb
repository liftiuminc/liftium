class FillsDay < ActiveRecord::Base

  set_table_name "fills_day"
  belongs_to :tag
end
