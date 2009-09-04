class CreateNetworks < ActiveRecord::Migration
  def self.up
    create_table :networks do |t|
      t.string :network_name
      t.string :pay_type
      t.timestamps
    end

    # Use add column because it supports better options
    add_column :networks, :enabled, :boolean, {:default => 1}
    add_column :networks, :supports_threshold, :boolean, {:default => 0}
    add_column :networks, :always_fill, :boolean, {:default => 1}
  end

  def self.down
    drop_table :networks
  end
end
