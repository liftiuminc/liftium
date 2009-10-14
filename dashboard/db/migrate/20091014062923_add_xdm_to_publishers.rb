class AddXdmToPublishers < ActiveRecord::Migration
  def self.up
	add_column :publishers, :xdm_iframe_path, :string
  end

  def self.down
	remove_column :publishers, :xdm_iframe_path
  end
end
