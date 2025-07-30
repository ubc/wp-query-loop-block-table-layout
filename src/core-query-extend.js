const { createHigherOrderComponent } = wp.compose;
const { useEffect } = wp.element;
const { useSelect } = wp.data;
const { BlockControls } = wp.blockEditor;
const { ToolbarGroup } = wp.components;
import { blockTable } from '@wordpress/icons';

function addAdditionalAttribute(settings, name) {
    if ('core/post-template' !== name) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            useTableLayout: {
                type: 'boolean',
                default: false
            }
        }
    }
}

wp.hooks.addFilter(
    'blocks.registerBlockType',
    'ubc/extension/table-layout/post-template/add-attributes',
    addAdditionalAttribute
);

const renderTableLayoutControl = createHigherOrderComponent((BlockEdit) => (props) => {
    if (props.name !== 'core/post-template') {
        return <BlockEdit {...props} />;
    }

    const { attributes, setAttributes } = props;
    const { layout, useTableLayout, className } = attributes;

    const childBlocks = useSelect(
        select => select('core/block-editor').getBlocks(props.clientId)
    );

    useEffect(() => {
        if (layout && '' !== layout) {
            // Remove is-table-layout from className.
            const newClassName = className ? className.replace(' is-table-layout', '') : '';
            setAttributes({
                useTableLayout: false,
                className: newClassName,
            });
        }
    }, [layout]);

    const classNameArray = className ? className.split(' ') : [];
    const displayTableControl = [
        {
            icon: blockTable,
            title: 'Table View',
            onClick: () => {
                setAttributes({
                    useTableLayout: ! classNameArray.includes('is-table-layout'),
                    className: classNameArray.includes('is-table-layout') ? classNameArray.filter(item => {
                        return item !== 'is-table-layout';
                    }).join(' ') : classNameArray.join(' ') + ' is-table-layout',
                    layout: ''
                });
            },
            isActive: useTableLayout
        }
    ];

    return (
        <>
            <BlockControls>
                <ToolbarGroup controls={displayTableControl} />
            </BlockControls>

            {useTableLayout && childBlocks.length > 0 ? (
                <>
                    <div className="ubc-table-layout-wrapper">
                        <div>
                            { childBlocks.map((block) => {
                                return <div key={block.clientId}>{( block.attributes.metadata &&block.attributes.metadata.name ? block.attributes.metadata.name : block.name )}</div>;
                            }) }
                        </div>
                    </div>
                    <BlockEdit {...props} />
                </>
            ) : ( <BlockEdit {...props} /> ) }
        </>
    );
}, 'ubc/extension/table-layout/post-template/add-controls');

wp.hooks.addFilter(
    'editor.BlockEdit',
    'ubc/extension/table-layout/post-template/add-controls',
    renderTableLayoutControl
);