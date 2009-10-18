class CreatePublisherNetworkLogins < ActiveRecord::Migration
  def self.up
    create_table :publisher_network_logins do |t|
      t.integer :network_id,        :null => false
      t.integer :publisher_id,      :null => false
      t.string :username,           :null => false
      t.string :password,           :null => false
      t.timestamps
    end
  end
  
  def self.down
    drop_table :publisher_network_logins
  end
end
