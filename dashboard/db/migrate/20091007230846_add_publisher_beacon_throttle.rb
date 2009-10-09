class AddPublisherBeaconThrottle < ActiveRecord::Migration
  def self.up
    add_column :publishers, :beacon_throttle, :float, :null => false, :default => 1.0
  end

  def self.down
    remove_column :publishers, :beacon_throttle
  end
end
