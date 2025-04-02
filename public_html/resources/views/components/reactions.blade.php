<div x-data="reactions(<?= $post['id'] ?>)">
    <div class="flex space-x-2">
        <template x-for="reaction in reactions" :key="reaction.name">
            <button 
                @click="react(reaction.name)"
                class="flex items-center px-2 py-1 rounded-full transition"
                :class="{
                    'bg-gray-100': userReaction === reaction.name,
                    'hover:bg-gray-50': userReaction !== reaction.name
                }"
                :title="reaction.count + ' ' + reaction.name"
            >
                <span class="text-lg" x-text="reaction.icon"></span>
                <span x-show="reaction.count > 0" 
                      class="ml-1 text-sm"
                      :style="{ color: reaction.color }"
                      x-text="reaction.count"></span>
            </button>
        </template>
    </div>
</div>