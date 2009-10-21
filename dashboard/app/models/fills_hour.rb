class FillsHour < FillsBase

  set_table_name "fills_hour"
  @table_name = "fills_hour"
  def time
    hour.strftime("%m/%d/%Y %H:00:00")
  end

  def time_column
    "hour"
  end

end
