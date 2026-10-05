@php($config = $this->shellConfig())
<div class="ae-workspace" wire:ignore x-data="aeWorkspace(@js($config))">
    {{ \App\Filament\Shell\Assets::script('workspace') }}
    <div class="ae-tabstrip" role="tablist" aria-label="{{ __('Open screens') }}">
        <template x-for="tab in tabs" :key="tab.id">
            <div
                class="ae-tab"
                role="tab"
                tabindex="0"
                x-bind:class="{ 'is-active': tab.id === active, 'is-pinned': tab.pinned }"
                x-bind:aria-selected="tab.id === active ? 'true' : 'false'"
                x-bind:title="tab.title"
                x-on:click="activate(tab.id)"
                x-on:keydown.enter="activate(tab.id)"
                x-on:mouseup.middle="close(tab.id)"
            >
                <span class="ae-tab-title" x-text="tab.title"></span>
                <button type="button" class="ae-tab-close" x-show="! tab.pinned" x-on:click.stop="close(tab.id)" x-bind:aria-label="labels.close">&times;</button>
            </div>
        </template>
    </div>
    <div class="ae-frames">
        <template x-for="tab in tabs" :key="tab.id">
            <iframe class="ae-frame" x-bind:src="tab.src" x-bind:title="tab.title" x-bind:data-tab="tab.id" x-show="tab.id === active"></iframe>
        </template>
    </div>
</div>
