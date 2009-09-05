class AddAdformatIdToTags < ActiveRecord::Migration
  def self.up
    add_column :tags, :adformat_id, :integer
    add_index :tags, :adformat_id
  end

  def self.down
    remove_column :tags, :adformat_id
  end
end
