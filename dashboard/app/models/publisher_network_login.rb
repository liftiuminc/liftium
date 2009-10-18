class PublisherNetworkLogin < ActiveRecord::Base
  belongs_to :network,      :class_name => "Network"
  belongs_to :publisher,    :class_name => "Publisher"

  validates_presence_of :username, :password, :network, :publisher

  attr_accessible :network_id, :publisher_id, :username, :password
end
