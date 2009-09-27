class TagOption < ActiveRecord::Base
  belongs_to :tag

  validates_uniqueness_of :option_name, { :scope => :tag_id }
  validates_numericality_of :tag_id
  validates_format_of :option_name, :with => /^[A-Za-z0-9_]+$/, :message => "Only alpha numeric characters allowed for option name"
  validates_presence_of :option_name
  validates_presence_of :option_value

  def name
	"#{option_name}"
  end
end
