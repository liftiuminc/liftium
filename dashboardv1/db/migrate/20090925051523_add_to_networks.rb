class AddToNetworks < ActiveRecord::Migration
  def self.up
    add_column :networks, :tag_template, :text
  end

  def self.down
    remove_column :networks, :tag_template
  end
end
