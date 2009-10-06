class CreateNetworkTagOptions < ActiveRecord::Migration
  def self.up
    create_table :network_tag_options do |t|
      t.references :network
      t.string :option_name
      t.boolean :required, {:default => false }
    end

    add_index :network_tag_options, [:network_id, :option_name], {:unique => true }
  end
  
  def self.down
    drop_table :network_tag_options
  end
end
