class CreatePublishers < ActiveRecord::Migration
  def self.up
    create_table :publishers do |t|
      t.string :publisher_name
      t.string :website
      t.timestamps
    end
  end

  def self.down
    drop_table :publishers
  end
end
