class CreateNetworkOptions < ActiveRecord::Migration
  def self.up
    create_table :network_options do |t|
      t.string :option_name
      t.references :network
    end
  end

  def self.down
    drop_table :network_options
  end
end
