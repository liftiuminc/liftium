class NetworkOption < ActiveRecord::Base
  belongs_to :network
  validates_uniqueness_of :option_name, { :scope => :network_id }
  validates_presence_of :network_id
  validates_numericality_of :network_id
  validates_presence_of :option_name
  validates_format_of :option_name, :with => /^[A-Za-z0-9_]+$/, :message => "Only alpha numeric characters allowed for option name"


  def name
	"#{option_name}"
  end
end
