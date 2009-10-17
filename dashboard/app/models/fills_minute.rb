class FillsMinute < FillsBase

  set_table_name "fills_minute"
  def time
    minute
  end

end
