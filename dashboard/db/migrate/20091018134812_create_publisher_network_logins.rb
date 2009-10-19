class CreatePublisherNetworkLogins < ActiveRecord::Migration
  def self.up
    create_table :publisher_network_logins do |t|
      t.integer :network_id,        :null => false
      t.integer :publisher_id,      :null => false
      t.string :username,           :null => false
      t.string :password,           :null => false
      t.timestamps
      t.references network
      t.references publisher
    end

    add_index "publisher_network_logins", ["network_id", "publisher_id"], :name => "index_publisher_network_logins_on_network_id_and_publisher_id", :unique => true
  end
  
  def self.down
    drop_table :publisher_network_logins
  end
end
