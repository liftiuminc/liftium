class CreatePublisherNetworkLogins < ActiveRecord::Migration
  def self.up
    create_table :publisher_network_logins do |t|
      t.references :network
      t.references :publisher
      t.string :username,           :null => false
      t.string :password,           :null => false
      t.timestamps
    end

    add_index "publisher_network_logins", ["network_id", "publisher_id"], :name => "index_publisher_network_logins_on_network_id_and_publisher_id", :unique => true
    add_index "publisher_network_logins", :publisher_id
  end
  
  def self.down
    drop_table :publisher_network_logins
  end
end
