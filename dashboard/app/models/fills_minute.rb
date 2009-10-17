class FillsMinute < FillsBase

  set_table_name "fills_minute"
  @table_name = "fills_minute"
  def time
    minute.strftime("%Y-%m-%d %H:%M:00")
  end

  def time_column
    "minute"
  end

end
