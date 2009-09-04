class AddindexToTags < ActiveRecord::Migration
  def self.up
    add_index(:tags, :network_id);
    add_index(:tags, :publisher_id);
  end

  def self.down
    remove_index(:tags, :network_id);
    remove_index(:tags, :publisher_id);
  end
end
