class FillsMinute < FillsBase

  set_table_name "fills_minute"
  @table_name = "fills_minute"
  def time
    minute.strftime("%m/%d/%Y %H:%M:00")
  end

  def time_column
    "minute"
  end

end
