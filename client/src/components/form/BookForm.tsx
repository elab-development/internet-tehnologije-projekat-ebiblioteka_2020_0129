import React, { useState } from 'react'
import { Genre } from '../../types'
import { Box, Button, Dialog, TextField, Typography } from '@mui/material'
import Select from '../Select'
import FileInput from './FileInput'

interface Props {
    genres: Genre[],
    open: boolean,
    onClose: () => void
}

const initialFormState = {
    name: '',
    isbn: '',
    writter: '',
    pages: null as number | null,
    published_year: null as number | null,
    genre_id: null as number | null,
    preview: '',
    content: '',
    description: '',
}

export default function BookForm(props: Props) {
    const [formState, setFormState] = useState(initialFormState)
    const onFieldChange = (name: keyof typeof initialFormState) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement> | string | number) => {
        setFormState(prev => {
            return {
                ...prev,
                [name]: (typeof e === 'string' || typeof e === 'number') ? e : e.target.value
            }
        })
    }
    return (
        <Dialog open={props.open} onClose={props.onClose} keepMounted>
            <Box sx={{
                width: '500px'
            }}>
                <Typography
                    variant='h5'
                    sx={{
                        padding: 2,
                        borderBottom: 1
                    }}
                >Create book form</Typography>
                <Box component='form' sx={{
                    padding: 2,
                    display: 'flex',
                    flexDirection: 'column',
                    gap: 1
                }}>
                    <TextField value={formState.name} onChange={onFieldChange('name')} InputLabelProps={{ shrink: true }} fullWidth label='Name' placeholder='Name' />
                    <TextField value={formState.isbn} onChange={onFieldChange('isbn')} InputLabelProps={{ shrink: true }} fullWidth label='ISBN' placeholder='ISBN' />
                    <TextField value={formState.writter} onChange={onFieldChange('writter')} InputLabelProps={{ shrink: true }} fullWidth label='Writter' placeholder='Writter' />
                    <TextField value={formState.pages} onChange={onFieldChange('pages')} InputLabelProps={{ shrink: true }} fullWidth type='number' label='Number of pages' placeholder='Number of pages' />
                    <TextField value={formState.published_year} onChange={onFieldChange('published_year')} InputLabelProps={{ shrink: true }} fullWidth type='number' label='Published year' placeholder='Published year' />
                    <FileInput value={formState.preview} label='Preview' onChange={onFieldChange('preview')} />
                    <FileInput value={formState.content} label='Content' onChange={onFieldChange('content')} />
                    <Select value={formState.genre_id} fullWidth placeholder='Genre' onChange={onFieldChange('genre_id')} options={props.genres.map(genre => {
                        return {
                            value: genre.id,
                            label: genre.name
                        }
                    })} />
                    <TextField value={formState.description} onChange={onFieldChange('description')} InputLabelProps={{ shrink: true }} fullWidth
                        label="Description"
                        placeholder="Description"
                        rows={4}
                        multiline
                    />
                    <Button variant='contained' color='primary' fullWidth type='submit'>Save book</Button>
                </Box>
            </Box>
        </Dialog>
    )
}
