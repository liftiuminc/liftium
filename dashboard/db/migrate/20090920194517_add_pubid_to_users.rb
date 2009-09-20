class AddPubidToUsers < ActiveRecord::Migration
  def self.up
    add_column :users, :publisher_id, :integer
  end

  def self.down
    remove_column :users, :publisher_id
  end
end
