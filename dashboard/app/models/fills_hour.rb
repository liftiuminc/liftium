class FillsHour < FillsBase

  set_table_name "fills_hour"
  def time
    hour
  end

end
