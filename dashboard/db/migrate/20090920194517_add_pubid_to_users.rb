class AddPubidToUsers < ActiveRecord::Migration
  def self.up
    add_column :users, :publisher_id, :number
  end

  def self.down
    remove_column :users, :publisher_id
  end
end
