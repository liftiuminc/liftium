class Addindexes < ActiveRecord::Migration
  def self.up
    add_index(:networks, :network_name, :unique => true);
    add_index(:adformats, :format_name, :unique => true);
    add_index(:publishers, :publisher_name, :unique => true);
    remove_index(:tags, :publisher_id);
    add_index(:tags, [:publisher_id, :tag_name], :unique => true);
    add_index(:users, :email, :unique => true);
  end

  def self.down
    remove_index(:networks, :network_name);
    remove_index(:adformats, :format_name);
    remove_index(:publishers, :publisher_name);
    remove_index(:users, :email);
    remove_index(:tags, [:publisher_id, :tag_name]);
    add_index(:tags, :publisher_id);
  end
end
