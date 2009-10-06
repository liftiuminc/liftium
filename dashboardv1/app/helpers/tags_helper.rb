module TagsHelper
  def size_form_column (record, input_name)
	select("", input_name, Adformat.all.collect {|af| [ af.name_with_size, af.size ] }, {:selected => record.size}, :include_blank => true) 
  end
end
