class TagTarget < ActiveRecord::Base
  belongs_to :tags

  validates_presence_of :key_name, :key_value
  validates_uniqueness_of :key_name, {:scope => :tag_id}

end
