module NetworksHelper
  # Pull this list from Networks.
  def pay_type_form_column (record, input_name)
     select("", input_name, ["Per Impression", "Per Click", "Affliate"], { :selected => record.pay_type, :include_blank => true })
  end
end
