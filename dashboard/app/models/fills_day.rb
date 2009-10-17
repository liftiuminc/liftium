class FillsDay < FillsBase

  set_table_name "fills_day"
  @table_name = "fills_day"
  def time
    day
  end

  def time_column
    "day"
  end

end
